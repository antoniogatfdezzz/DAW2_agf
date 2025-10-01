from flask import Flask, render_template

app = Flask(__name__, template_folder="../frontend/templates", static_folder="../frontend/static")

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

@app.route("/contact")
def contact():
    return render_template("contact.html")

def get_movie_by_id(movie_id: int):
    return next((movie for movie in movies if movie['id'] == movie_id), None)

@app.route("/movie/<int:mid>")
def movie_detail(mid):
    movie = get_movie_by_id(mid)
    if not movie:
        from flask import redirect, url_for
        return redirect(url_for("movies"))
     return render_template("movie_detail.html", movie=movie)


@app.route("/movies", endpoint="movies")
def movies_page():
    return render_template("movies.html", movies=movies)

@app.route("/about")
def about():
    return render_template("about.html")

@app.route("/")
def index():
#    return "<h1>Antonio Gat Fernández</h1>\n<h2>2º DAW</h2>\n<p>Hello, World!</p>"
    return render_template("index.html")


if __name__ == "__main__":
    app.run(debug=True)








#print("Hello, World!")
#    print(hola.__class__)
#
#    if True:
#    print("Si...... si")

#private class Hello {
#    public static void main() {
#        System.out.println("Hello");
#    }
#}