# Establecemos la imagen base de Python
FROM python:bookworm

# Definimos el directorio de trabajo
WORKDIR /app

# Copiamos los archivos de requerimientos y la aplicación
COPY requirements.txt ./
COPY app.py ./

# Instalamos las dependencias
RUN pip install --no-cache-dir -r requirements.txt

# Variables de entorno para que Flask arranque correctamente en contenedor
ENV FLASK_APP=app.py
ENV FLASK_RUN_HOST=0.0.0.0
ENV FLASK_RUN_PORT=5000

# Exponemos el puerto 5000 para comunicarnos con la aplicación
EXPOSE 5000

# Definimos el comando de arranque
CMD ["python", "-m", "flask", "run"]
