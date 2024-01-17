<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Carpeta;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Resources\Carpeta\CarpetaResource;
// use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Facades\Image as InterventionImage;
use Intervention\Image\Exception\NotReadableException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;

class MongoController extends ApiController
{
    protected $mongoClient;
    protected $foldersCollection;
    protected $filesCollection;

    public function __construct()
    {
        $mongoUser = config('services.mongo.user');
        $mongoPassword = config('services.mongo.password');
        $mongoHost = config('services.mongo.host');
        $mongoPort = config('services.mongo.port');
        $encodedPassword = urlencode($mongoPassword);

        $connectionString = "mongodb://$mongoUser:$encodedPassword@$mongoHost:$mongoPort";

        $this->mongoClient = new \MongoDB\Client($connectionString);
        $this->foldersCollection = $this->mongoClient->selectDatabase('ccead_bd')->selectCollection('folders');
        $this->filesCollection = $this->mongoClient->selectDatabase('ccead_bd')->selectCollection('fs.files');
        $this->chunksCollection = $this->mongoClient->selectDatabase('ccead_bd')->selectCollection('fs.chunks');
    }

    public function getFoldersByBox(Request $request)
    {
        $data = $this->foldersCollection->find(['box_id' => (int)$request->box_id])->toArray();

        return response()->json($data);
    }

    public function getFilesByFolderId(Request $request)
    {
        $folder = $this->foldersCollection->findOne(['folder_id' => (int)$request->id]);

        if (!$folder) {

            return response()->json(['success' => false], 404);

        } else {
            $folderId = $folder->_id;

            $folderData = Carpeta::find($request->id);

            $data = $this->filesCollection->find(['general_doc_id' => (string)$folderId])->toArray();

            return response()->json(['data' => $data, 'folder' => new CarpetaResource($folderData)]);
        }
    }

    public function deleteFileById(Request $request)
    {
        $fileIds = $request->file_ids;

        foreach ($fileIds as $fileId) {
            $deletedFile = $this->filesCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($fileId)]);

            if ($deletedFile) {
                $generalDocId = $deletedFile->general_doc_id;

                // Obtener todos los documentos con el mismo general_doc_id sin ordenar
                $filesToUpdate = $this->filesCollection->find(['general_doc_id' => $generalDocId]);

                // Convertir el cursor a un array y ordenar por order
                $filesToUpdateArray = iterator_to_array($filesToUpdate);
                usort($filesToUpdateArray, function ($a, $b) {
                    return $a->order <=> $b->order;
                });

                // Inicializar un contador para el nuevo orden
                $newOrder = 1;

                // Recorrer y actualizar los valores de order en orden secuencial
                foreach ($filesToUpdateArray as $fileToUpdate) {
                    if ($fileToUpdate->_id == $deletedFile->_id) {
                        // Si es el documento que estamos eliminando, no lo actualizamos
                        continue;
                    }

                    $this->filesCollection->updateOne(
                        ['_id' => $fileToUpdate->_id],
                        ['$set' => ['order' => $newOrder]]
                    );
                    $newOrder++;
                }

                // Eliminar el documento principal de fs.files
                $this->filesCollection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($fileId)]);

                // Eliminar los documentos relacionados en fs.chunks
                $this->chunksCollection->deleteMany(['files_id' => new \MongoDB\BSON\ObjectId($fileId)]);
            }
        }

        return response()->json(['message' => 'Eliminación exitosa.']);
    }

    public function updateFileById(Request $request)
    {
        $filesToUpdate = $request->input('files');
        $selectedIds = $request->input('selected');
        $type = $request->input('type');

        // Obtener todos los documentos con el mismo general_doc_id
        $generalDocId = $filesToUpdate[0]['general_doc_id']; // Tomamos el general_doc_id del primer elemento
        $filesToUpdateCursor = $this->filesCollection->find(['general_doc_id' => $generalDocId]);

        // Convertir el cursor a un array
        $filesToUpdateArray = iterator_to_array($filesToUpdateCursor);

        // Ordenar el array según el nuevo orden
        usort($filesToUpdateArray, function ($a, $b) use ($filesToUpdate) {
            $idA = (string) $a->_id;
            $idB = (string) $b->_id;

            // Obtener el nuevo order de cada documento del segundo arreglo
            $orderA = collect($filesToUpdate)->firstWhere('_id.$oid', $idA)['order'];
            $orderB = collect($filesToUpdate)->firstWhere('_id.$oid', $idB)['order'];

            return $orderA <=> $orderB;
        });

        // Borrar los documentos existentes con el mismo general_doc_id
        $this->filesCollection->deleteMany(['general_doc_id' => $generalDocId]);

        // Insertar los documentos ordenados en la colección
        foreach ($filesToUpdateArray as $index => $fileToUpdate) {
            $fileToUpdate->order = $index + 1;
            $this->filesCollection->insertOne($fileToUpdate);
        }

        // Verificar si se proporcionaron ids seleccionados y el tipo no es nulo
        if (!empty($selectedIds) && !is_null($type)) {
            // Convertir las cadenas de IDs a objetos ObjectId
            $selectedIds = array_map(function ($id) {
                return new \MongoDB\BSON\ObjectId($id);
            }, $selectedIds);

            // Actualizar la columna 'group' de los documentos seleccionados
            $this->filesCollection->updateMany(
                ['_id' => ['$in' => $selectedIds]],
                ['$set' => ['group' => $type]]
            );
        }

        // Obtener los documentos ordenados después de la actualización
        $filesOrderedCursor = $this->filesCollection->find(['general_doc_id' => $generalDocId]);

        // Convertir el cursor a un array
        $filesOrderedArray = iterator_to_array($filesOrderedCursor);

        return response()->json(['message' => 'Actualización exitosa.']);
    }

    public function saveFileDescription(Request $request)
    {
        // Convertir la cadena de _id a un objeto ObjectId
        $fileIdObject = new \MongoDB\BSON\ObjectId($request->file_id);

        // Realizar la actualización
        $this->filesCollection->updateOne(
            ['_id' => $fileIdObject],
            ['$set' => ['description' => $request->description]]
        );

        return response()->json(['message' => 'Actualización exitosa.']);
    }

    public function getFileFolder(Request $request)
    {
        // Realizar la búsqueda
        $cursor = $this->filesCollection->find([
            'description' => ['$regex' => $request->tag, '$options' => 'i'],
            'general_doc_id' => $request->folder_id
        ]);

        // Convertir el cursor a un array de documentos
        $matchingDocuments = iterator_to_array($cursor);

        // Devolver los documentos que coinciden con la búsqueda
        return response()->json(['data' => $matchingDocuments]);
    }

    public function generateAndDownloadPdfZ1(Request $request)
    {
        $script = config('services.python.dig1');

        try {
            $folder_id = $request->input('folder_id');

            $aduana = $request->input('aduana');

            $dui = $request->input('dui');

            $year = $request->input('year');

            $agencia = $request->input('agencia');

            $agenciaArgument = escapeshellarg($agencia);

            shell_exec("python $script $folder_id $dui $year $aduana $agenciaArgument");

            $zipPath = "C:/laragon/www/ccead_backend/agencias/{$agencia}/temp_pdfs_1/{$year}_{$dui}.zip";

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function generateAndDownloadPdfZ2(Request $request)
    {
        $script = config('services.python.dig2');

        try {
            $folder_id = $request->input('folder_id');

            $aduana = $request->input('aduana');

            $dui = $request->input('dui');

            $year = $request->input('year');

            $agencia = $request->input('agencia');

            $agenciaArgument = escapeshellarg($agencia);

            shell_exec("python $script $folder_id $dui $year $aduana $agenciaArgument");

            $zipPath = "C:/laragon/www/ccead_backend/agencias/{$agencia}/temp_pdfs_2/{$year}_{$dui}.zip";

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }
}