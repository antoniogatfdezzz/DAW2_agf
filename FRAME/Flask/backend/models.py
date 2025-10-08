import sqlite3

def save_contact(name, email, subject, message):
    pass

def get_movies():
    connection = sqlite3.connect('backend/peliculas.db')
    cursor = connection.cursor()

    cursor.execute("SELECT * ORDER BY createrd_at DESC)
    movies = cursor.fetchall()

    connection.commit()
    connection.close()
    return movies

def get_movie_by_id(movie_id):
    connection = sqlite3.connect('backend/peliculas.db')
    cursor = connection.cursor()

    cursor.execute("SELECT * FROM peliculas WHERE id = ?", (movie_id,))
    movie = cursor.fetchone()

    connection.commit()
    connection.close()
    return movie


    connection.commit()
    connection.close()
    return movies

def init_db():
    conn = sqlite3.connect('peliculas.db')
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

def add_movies():
    conn = sqlite3.connect('backend/peliculas.db')
    cursor = conn.cursor()

    movies = [
        {'id': 1, 
     'title': 'The Shawshank Redemption',
     'director': 'Frank Darabont',
     'year': 1994,
     'genre': 'Drama',
     'description': 'Two imprisoned',
     'rating': 9.3,
     'poster_url': 'https://m.media-amazon.com/images/M/MV5BMDFkYTc0MGEtZmNhMC00ZDIzLWFmNTEtODM1ZmRlYWMwMWFmXkEyXkFqcGdeQXVyNDYyMDk5MTU@._V1_FMjpg_UX1000_.jpg'
     },

    {'id': 2, 
     'title': 'The Godfather',
     'director': 'Francis Ford Coppola',
     'year': 1972,
     'genre': 'Crime',
     'description': 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
     'rating': 9.2,
     'poster_url': 'https://m.media-amazon.com/images/M/MV5BMDFkYTc0MGEtZmNhMC00ZDIzLWFmNTEtODM1ZmRlYWMwMWFmXkEyXkFqcGdeQXVyNDYyMDk5MTU@._V1_FMjpg_UX1000_.jpg'
     }
    ]

cur    



if __name__ == "__main__":
    print("")