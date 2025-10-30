function ContactPage() {
  return (
    <div className="row justify-content-center">
      <div className="col-12 col-md-8 col-lg-6">
        <h2>Contacto</h2>
        <form className="vstack gap-3">
          <div>
            <label htmlFor="name" className="form-label">Nombre</label>
            <input id="name" type="text" className="form-control" placeholder="Tu nombre" />
          </div>
          <div>
            <label htmlFor="email" className="form-label">Email</label>
            <input id="email" type="email" className="form-control" placeholder="tu@email.com" />
          </div>
          <div>
            <label htmlFor="message" className="form-label">Mensaje</label>
            <textarea id="message" className="form-control" rows={4} placeholder="¿En qué podemos ayudarte?"></textarea>
          </div>
          <button type="button" className="btn btn-primary">Enviar</button>
        </form>
      </div>
    </div>
  )
}

export default ContactPage
