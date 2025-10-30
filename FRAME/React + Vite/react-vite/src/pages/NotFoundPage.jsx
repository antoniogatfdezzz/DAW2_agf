import { Link } from 'react-router-dom'

function NotFoundPage() {
  return (
    <div className="text-center">
      <h2>404 - Página no encontrada</h2>
      <p className="text-muted">La ruta que has solicitado no existe.</p>
      <Link to="/" className="btn btn-secondary">Ir al inicio</Link>
    </div>
  )
}

export default NotFoundPage
