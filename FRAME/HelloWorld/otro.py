
import datetime
from libreria_interna import hola


hoy = datetime.date.today()
print("Hola, hoy es", hoy)
print(hoy.__class__)

if hoy=="2025-09-24":
    print("Hoy es 24 de septiembre de 2025")
else:
    print("no se que dia es")

hola()

