import os
import shutil
import sys
import io
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from PIL import Image
from pymongo import MongoClient
from gridfs import GridFS
from bson.objectid import ObjectId
import zipfile
from dotenv import load_dotenv
from urllib.parse import quote_plus
load_dotenv()

mongo_user = os.getenv('MONGO_USER', '')
mongo_password = os.getenv('MONGO_PASSWORD', '')
mongo_host = os.getenv('MONGO_HOST', '')
mongo_port = os.getenv('MONGO_PORT', '')

mongo_path = os.getenv('MONGO_PATH', '')

connection_string = f"mongodb://{quote_plus(mongo_user)}:{quote_plus(mongo_password)}@{mongo_host}:{mongo_port}/"

# Configuración de MongoDB
client = MongoClient(connection_string)
db = client['ccead_bd']
fs = GridFS(db)

def convert_tiff_to_jpg(tiff_data, quality=15, resize=None):
    # Convertir a modo RGB antes de guardar como JPEG
    tiff_data = tiff_data.convert("RGB")

    # Redimensionar la imagen si se especifica
    if resize:
        tiff_data.thumbnail(resize)

    # Crear un objeto BytesIO para almacenar la imagen JPEG
    jpg_buffer = io.BytesIO()

    # Guardar la imagen TIFF como JPEG en el objeto BytesIO
    tiff_data.save(jpg_buffer, format="JPEG", optimize=True, quality=quality)

    return jpg_buffer.getvalue()

def reconstruct_tiff_to_jpg(folder_id, agencia):
    # Obtener el documento folders según el _id proporcionado
    folder_doc = db['folders'].find_one({'_id': ObjectId(folder_id)})

    if not folder_doc:
        print(f"No se encontró el documento folders con el _id {folder_id}")
        return None

    # Crear un directorio para almacenar las imágenes JPEG
    output_directory = f"{mongo_path}/{agencia}/temp_images_1"
    os.makedirs(output_directory, exist_ok=True)

    # Buscar los documentos fs.files relacionados con el folder_id
    file_docs = list(fs.find({'general_doc_id': str(folder_doc['_id'])}))

    # Ordenar los documentos por el campo "order"
    file_docs.sort(key=lambda x: x.order or 1)

    for i, file_doc in enumerate(file_docs):
        # Obtener el contenido binario del archivo
        file_content = file_doc.read()

        # Intentar abrir el archivo TIFF como imagen
        try:
            tiff_image = Image.open(io.BytesIO(file_content))
        except Exception as e:
            print(f"Error al abrir el archivo TIFF como imagen: {str(e)}")
            continue

        # Convertir la página TIFF a JPEG con compresión adicional
        jpg_data = convert_tiff_to_jpg(tiff_image, quality=15, resize=(1600, 1600))

        # Guardar la imagen JPEG en un archivo con el nombre basado en el índice (i)
        if jpg_data:
            jpg_filename = f"{output_directory}/page_{i + 1}.jpg"
            with open(jpg_filename, "wb") as jpg_file:
                jpg_file.write(jpg_data)

            print(f"Imagen {jpg_filename} generada y guardada.")

    return output_directory

def create_pdf_from_images(dui, year, aduana, agencia):
    # Directorio donde se guardaron las imágenes JPEG
    input_directory = f"{mongo_path}/{agencia}/temp_images_1"

    # Nombre del directorio de salida
    output_directory = f"{mongo_path}/{agencia}/temp_pdfs_1/{aduana}/{year}/{dui}"

    # Crear el directorio de salida si no existe
    os.makedirs(output_directory, exist_ok=True)

    # Nombre del archivo PDF de salida
    output_pdf_path = f"{output_directory}/{dui}.pdf"

    # Crear un objeto Canvas para el PDF
    pdf_canvas = canvas.Canvas(output_pdf_path, pagesize=letter)

    # Obtener la lista de nombres de archivos en el directorio de entrada
    image_files = sorted(os.listdir(input_directory))

    # Agregar cada imagen al archivo PDF, ordenándolas por su índice en el nombre del archivo
    for i, file_name in enumerate(sorted(image_files, key=lambda x: int(x.split("_")[1].split(".")[0]))):
        if file_name.endswith(".jpg"):
            image_path = os.path.join(input_directory, file_name)

            # Abrir la imagen y redimensionar si es necesario
            img = Image.open(image_path)
            img.thumbnail((1600, 1600))

            # Agregar la imagen al PDF en la posición (0, 0) y ajustar según sea necesario
            pdf_canvas.drawImage(image_path, 0, 0, width=letter[0], height=letter[1], preserveAspectRatio=True, mask='auto')

            # Agregar una nueva página para la siguiente imagen
            pdf_canvas.showPage()

    # Guardar y cerrar el archivo PDF
    pdf_canvas.save()

    print(f"PDF combinado creado y guardado en {output_pdf_path}")

    # Cambiar al directorio temp_pdfs y comprimir
    os.chdir(f"{mongo_path}/{agencia}/temp_pdfs_1")
    with zipfile.ZipFile(f"{year}_{dui}.zip", 'w') as zipf:
        zipf.write(f"{aduana}/{year}/{dui}/{dui}.pdf", arcname=f"{aduana}/{year}/{dui}/{dui}.pdf")

    # Eliminar directorios y archivos temporales
    shutil.rmtree(f"{mongo_path}/{agencia}/temp_pdfs_1/{aduana}")
    

if __name__ == "__main__":
    # Recibir el _id de folders desde PHP
    folder_id = sys.argv[1] if len(sys.argv) > 1 else "65a18b6241dba120ec4924cc"
    dui = sys.argv[2] if len(sys.argv) > 2 else "20"
    year = sys.argv[3] if len(sys.argv) > 2 else "2014"
    aduana = sys.argv[4] if len(sys.argv) > 2 else "701"
    agencia = sys.argv[5] if len(sys.argv) > 2 else "CCEAD SA"

    agencia_safe = agencia.replace(' ', '_')

    # Llamar a la función para reconstruir los TIFF a JPEG con compresión adicional
    image_directory = reconstruct_tiff_to_jpg(folder_id, agencia_safe)

    # Llamar a la función para crear el PDF combinado y comprimir la carpeta
    create_pdf_from_images(dui, year, aduana, agencia_safe)
    shutil.rmtree(f'{mongo_path}/{agencia_safe}/temp_images_1')
