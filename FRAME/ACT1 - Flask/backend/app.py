from flask import Flask, render_template, request, flash, redirect, url_for, abort
from models import save_contact, get_movies, get_movie_by_id, add_movie as insert_movie

app = Flask(__name__, template_folder="../frontend/templates", static_folder="../frontend/static")
app.config['SECRET_KEY'] = 'ramdom-key'

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/movies", endpoint="movies")
def movies_page():
    movies = get_movies()
    return render_template("movies.html", movies=movies)

@app.route("/movie/<int:mid>")
def movie_detail(mid):
    movie = get_movie_by_id(mid)
    if not movie:
        return redirect(url_for("movies"))
    return render_template("movie_detail.html", movie=movie)

@app.route("/add_movie", methods=["GET", "POST"])
def add_movie():
    if request.method == "POST":
        title = request.form.get("title")
        director = request.form.get("director")
        year = request.form.get("year")
        genre = request.form.get("genre")
        description = request.form.get("description")
        rating = request.form.get("rating")
        poster_url = request.form.get("poster_url")
        
        if not all([title, director, year, genre, description, rating, poster_url]):
            flash("Todos los campos son obligatorios", "error")
            return render_template("add_movie.html")
        
        try:
            success = insert_movie(title, director, int(year), genre, description, float(rating), poster_url)
            if success:
                flash(f"¡Película '{title}' añadida correctamente!", "success")
                return redirect(url_for("movies"))
            else:
                flash("Error al añadir la película. Inténtalo de nuevo.", "error")
        except ValueError:
            flash("Error en el formato de los datos. Verifica el año y la valoración.", "error")
        except Exception as e:
            flash(f"Error inesperado: {str(e)}", "error")
        
        return render_template("add_movie.html")
    
    return render_template("add_movie.html")

@app.route("/about")
def about():
    return render_template("about.html")

@app.route("/contact", methods=["GET", "POST"])
def contact():
    if request.method == "POST":
        name = request.form.get("name")
        email = request.form.get("email")
        subject = request.form.get("subject")
        message = request.form.get("message")
        
        if not all([name, email, subject, message]):
            flash("Todos los campos son obligatorios", "error")
            return render_template("contact.html")
        
        success = save_contact(name, email, subject, message)
        if success:
            flash("¡Mensaje enviado correctamente!", "success")
            return redirect(url_for("index"))
        else:
            flash("Error al enviar el mensaje. Inténtalo de nuevo.", "error")
    
    return render_template("contact.html")


if __name__ == "__main__":
    app.run(debug=True)
