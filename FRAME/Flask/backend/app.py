from flask import Flask, render_template, request, flash, redirect, url_for, abort
from models import save_contact, get_movies

app = Flask(__name__, template_folder="../frontend/templates", static_folder="../frontend/static")
app.config ['SECRET_KEY'] = 'ramdom- key'

movie_list = get_movies()

@app.route("/contact")
def contact():
    if request.method == "POST":
        name = request.form.get("name")
        email = request.form.get("email")
        subject = request.form.get("subject")
        message = request.form.get("message")
        
        

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