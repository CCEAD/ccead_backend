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
import pymysql
from dotenv import load_dotenv
from urllib.parse import quote_plus

load_dotenv()

mongo_user = os.getenv('MONGO_USER', '')
mongo_password = os.getenv('MONGO_PASSWORD', '')
mongo_host = os.getenv('MONGO_HOST', '')
mongo_port = os.getenv('MONGO_PORT', '')

mysql_user = os.getenv('DB_USERNAME', '')
mysql_password = os.getenv('DB_PASSWORD', '')
mysql_host = os.getenv('DB_HOST', '')
mysql_db = os.getenv('DB_DATABASE', '')

mongo_path = os.getenv('MONGO_PATH', '')

connection_string = f"mongodb://{quote_plus(mongo_user)}:{quote_plus(mongo_password)}@{mongo_host}:{mongo_port}/"

# Configuración de MongoDB
client = MongoClient(connection_string)
db = client['ccead_bd']
fs = GridFS(db)

def get_group_mapping_from_database():
    try:
        # Conectar a la base de datos MySQL
        connection = pymysql.connect(
            host=mysql_host,
            user=mysql_user,
            password=mysql_password,
            database=mysql_db,
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )

        with connection.cursor() as cursor:
            # Consultar los datos de la tabla grupos
            cursor.execute("SELECT id, name FROM grupos")
            group_mapping_data = cursor.fetchall()

    except Exception as e:
        print(f"Error al obtener datos de la tabla grupos: {str(e)}")
        group_mapping_data = []

    finally:
        # Cerrar la conexión a la base de datos
        if connection:
            connection.close()

    return group_mapping_data

def convert_tiff_to_jpg(tiff_data, quality=15, resize=None):
    # Convertir a modo RGB antes de guardar como JPEG
    tiff_data = tiff_data.convert("RGB")

    # Redimensionar la imagen si se especifica
    if resize:
        tiff_data.thumbnail(resize)

    # Crear un objeto BytesIO para almacenar las imagen JPEG
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
    output_directory = f"{mongo_path}/{agencia}/temp_images_2"
    os.makedirs(output_directory, exist_ok=True)

    # Buscar los documentos fs.files relacionados con el folder_id
    file_docs = list(fs.find({'general_doc_id': str(folder_doc['_id'])}))

    for i, file_doc in enumerate(file_docs):
        # Obtener el contenido binario del archivo
        file_content = file_doc.read()

        # Intentar abrir el archivo TIFF como imagen
        try:
            tiff_image = Image.open(io.BytesIO(file_content))
        except Exception as e:
            print(f"Error al abrir el archivo TIFF como imagen: {str(e)}")
            continue

        # Obtener el orden y grupo del archivo
        order = int(file_doc.order)
        group = int(file_doc.group)

        # Convertir la página TIFF a JPEG con compresión adicional
        jpg_data = convert_tiff_to_jpg(tiff_image, quality=15, resize=(1600, 1600))

        # Guardar la imagen JPEG en un archivo con el nombre adecuado
        jpg_filename = f"{output_directory}/{group}_page_{order}.jpg"
        with open(jpg_filename, "wb") as jpg_file:
            jpg_file.write(jpg_data)

        print(f"Imagen {jpg_filename} generada y guardada.")

    return output_directory

def create_pdfs_by_group(dui, year, aduana, group_mapping, agencia):
    # Directorio donde se guardaron las imágenes JPEG
    input_directory = f"{mongo_path}/{agencia}/temp_images_2"

    # Crear el directorio de imagenes
    os.makedirs(input_directory, exist_ok=True)

    # Nombre del directorio de salida
    output_directory_base = f"{mongo_path}/{agencia}/temp_pdfs_2/{aduana}/{year}/{dui}"

    # Crear el directorio base si no existe
    os.makedirs(output_directory_base, exist_ok=True)

    # Obtener la lista de nombres de archivos en el directorio de entrada
    image_files = sorted(os.listdir(input_directory))

    # Agrupar archivos por el valor del campo "group"
    files_by_group = {}
    for image_file in image_files:
        image_path = os.path.join(input_directory, image_file)

        # Obtener el número de grupo desde el nombre del archivo
        group = int(image_file.split("_")[0])

        files_by_group.setdefault(group, []).append(image_file)

    # Crear directorios y PDFs para cada grupo
    for group, group_files in files_by_group.items():
        # Verificar si el grupo está en el mapeo
        if group in group_mapping:
            # Obtener el nombre del grupo
            group_name = group_mapping[group].replace(' ', '_')

            # Crear el directorio para el grupo
            output_directory = os.path.join(output_directory_base, aduana, year, dui, group_name)
            os.makedirs(output_directory, exist_ok=True)

            # Crear un objeto Canvas para el PDF
            pdf_canvas = canvas.Canvas(os.path.join(output_directory, f"{group_name}.pdf"), pagesize=letter)

            # Agrupar archivos por el valor del campo "order"
            group_files.sort(key=lambda x: int(x.split("_")[2].split(".")[0]))

            # Agregar cada imagen al archivo PDF
            for group_file in group_files:
                image_path = os.path.join(input_directory, group_file)

                # Abrir la imagen y redimensionar si es necesario
                img = Image.open(image_path)
                img.thumbnail((1600, 1600))

                # Agregar la imagen al PDF en la posición (0, 0) y ajustar según sea necesario
                pdf_canvas.drawImage(image_path, 0, 0, width=letter[0], height=letter[1], preserveAspectRatio=True, mask='auto')
                pdf_canvas.showPage()

            # Guardar y cerrar el archivo PDF
            pdf_canvas.save()

    # Comprimir todos los directorios en un archivo ZIP
    zip_filename = f"{mongo_path}/{agencia}/temp_pdfs_2/{year}_{dui}"
    shutil.make_archive(zip_filename, 'zip', output_directory_base)

    print(f"Archivos ZIP generados en {zip_filename}")

     # Eliminar directorios y archivos temporales
    shutil.rmtree(f"{mongo_path}/{agencia}/temp_pdfs_2/{aduana}")

if __name__ == "__main__":
    # Recibir el _id de folders desde PHP
    folder_id = sys.argv[1] if len(sys.argv) > 1 else "65a18b6241dba120ec4924cc"
    dui = sys.argv[2] if len(sys.argv) > 2 else "20"
    year = sys.argv[3] if len(sys.argv) > 2 else "2014"
    aduana = sys.argv[4] if len(sys.argv) > 2 else "701"
    agencia = sys.argv[5] if len(sys.argv) > 2 else "CCEAD SA"

    agencia_safe = agencia.replace(' ', '_')

    group_mapping_data = get_group_mapping_from_database()

    # Convertir el resultado a un diccionario
    group_mapping = {entry['id']: entry['name'] for entry in group_mapping_data}

    # Llamar a la función para reconstruir los TIFF a JPEG con compresión adicional
    image_directory = reconstruct_tiff_to_jpg(folder_id, agencia_safe)

    # Llamar a la función para crear el PDF combinado y comprimir la carpeta
    create_pdfs_by_group(dui, year, aduana, group_mapping, agencia_safe)

    # Eliminar directorio de imágenes
    shutil.rmtree(f'{mongo_path}/{agencia_safe}/temp_images_2')
