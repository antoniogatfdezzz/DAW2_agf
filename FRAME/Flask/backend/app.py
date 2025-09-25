from flask import Flask, render_template

app = Flask(__name__, template_folder="../frontend/templates")

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