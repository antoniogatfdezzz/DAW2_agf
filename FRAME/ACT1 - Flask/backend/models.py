import sqlite3
import os

# Obtener la ruta absoluta del directorio del script
DB_PATH = os.path.join(os.path.dirname(__file__), 'peliculas.db')

def save_contact(name, email, subject, message):
    """
    Guarda un mensaje de contacto en la base de datos.
    Retorna True si se guardó correctamente, False en caso contrario.
    """
    try:
        connection = sqlite3.connect(DB_PATH)
        cursor = connection.cursor()

        cursor.execute('''
            INSERT INTO contactos (name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ''', (name, email, subject, message))

        connection.commit()
        connection.close()
        return True
    except Exception as e:
        print(f"Error al guardar contacto: {e}")
        return False

def get_movies():
    connection = sqlite3.connect(DB_PATH)
    connection.row_factory = sqlite3.Row  # Para obtener diccionarios en lugar de tuplas
    cursor = connection.cursor()

    cursor.execute("SELECT * FROM peliculas ORDER BY created_at DESC")
    movies = cursor.fetchall()

    connection.close()
    # Convertir Row objects a diccionarios
    return [dict(movie) for movie in movies]

def get_movie_by_id(movie_id):
    connection = sqlite3.connect(DB_PATH)
    connection.row_factory = sqlite3.Row  # Para obtener diccionarios en lugar de tuplas
    cursor = connection.cursor()

    cursor.execute("SELECT * FROM peliculas WHERE id = ?", (movie_id,))
    movie = cursor.fetchone()

    connection.close()
    return dict(movie) if movie else None

def add_movie(title, director, year, genre, description, rating, poster_url):
    """
    Añade una nueva película a la base de datos.
    Retorna True si se añadió correctamente, False en caso contrario.
    """
    try:
        connection = sqlite3.connect(DB_PATH)
        cursor = connection.cursor()

        cursor.execute('''
            INSERT INTO peliculas (title, director, year, genre, description, rating, poster_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (title, director, year, genre, description, rating, poster_url))

        connection.commit()
        connection.close()
        return True
    except Exception as e:
        print(f"Error al añadir película: {e}")
        return False


    connection.commit()
    connection.close()
    return movies

def init_db():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()

    # TABLAS DE CONTACTOS
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS contactos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    # TABLAS DE PELICULAS
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS peliculas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            director TEXT NOT NULL,
            year INTEGER NOT NULL,
            genre TEXT NOT NULL,
            description TEXT NOT NULL,
            rating REAL NOT NULL,
            poster_url TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ''')

    conn.commit()
    conn.close()

def add_sample_movies():
    """Añade películas de ejemplo a la base de datos"""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()

    # Verificar si ya hay películas
    cursor.execute("SELECT COUNT(*) FROM peliculas")
    count = cursor.fetchone()[0]
    
    if count > 0:
        print(f"Ya existen {count} películas en la base de datos")
        conn.close()
        return

    movies = [
        {
            'title': 'The Shawshank Redemption',
            'director': 'Frank Darabont',
            'year': 1994,
            'genre': 'Drama',
            'description': 'Dos hombres encarcelados forjan una amistad a lo largo de los años, encontrando consuelo y eventual redención a través de actos de decencia común.',
            'rating': 9.3,
            'poster_url': 'https://m.media-amazon.com/images/M/MV5BNDE3ODcxYzMtY2YzZC00NmNlLWJiNDMtZDViZWM2MzIxZDYwXkEyXkFqcGdeQXVyNjAwNDUxODI@._V1_.jpg'
        },
        {
            'title': 'The Godfather',
            'director': 'Francis Ford Coppola',
            'year': 1972,
            'genre': 'Crimen',
            'description': 'El patriarca envejecido de una dinastía del crimen organizado transfiere el control de su imperio clandestino a su reticente hijo.',
            'rating': 9.2,
            'poster_url': 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
        },
        {
            'title': 'The Dark Knight',
            'director': 'Christopher Nolan',
            'year': 2008,
            'genre': 'Acción',
            'description': 'Cuando la amenaza conocida como el Joker emerge del hampa, causa estragos y caos en la gente de Gotham. Batman debe aceptar una de las mayores pruebas psicológicas y físicas.',
            'rating': 9.0,
            'poster_url': 'https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_.jpg'
        },
        {
            'title': 'Pulp Fiction',
            'director': 'Quentin Tarantino',
            'year': 1994,
            'genre': 'Crimen',
            'description': 'Las vidas de dos sicarios de la mafia, un boxeador, la esposa de un gánster y dos bandidos de restaurantes se entrelazan en cuatro cuentos de violencia y redención.',
            'rating': 8.9,
            'poster_url': 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg'
        },
        {
            'title': 'Forrest Gump',
            'director': 'Robert Zemeckis',
            'year': 1994,
            'genre': 'Drama',
            'description': 'Las presidencias de Kennedy y Johnson, la guerra de Vietnam, el escándalo Watergate y otros eventos se desarrollan a través de la perspectiva de un hombre de Alabama con un coeficiente intelectual de 75.',
            'rating': 8.8,
            'poster_url': 'https://m.media-amazon.com/images/M/MV5BNWIwODRlZTUtY2U3ZS00Yzg1LWJhNzYtMmZiYmEyNmU1NjMzXkEyXkFqcGdeQXVyMTQxNzMzNDI@._V1_.jpg'
        }
    ]

    for movie in movies:
        cursor.execute('''
            INSERT INTO peliculas (title, director, year, genre, description, rating, poster_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (movie['title'], movie['director'], movie['year'], 
              movie['genre'], movie['description'], movie['rating'], movie['poster_url']))

    conn.commit()
    conn.close()
    print(f"Se añadieron {len(movies)} películas de ejemplo")


if __name__ == "__main__":
    init_db()
    add_sample_movies()
    print("✅ Base de datos inicializada correctamente")
