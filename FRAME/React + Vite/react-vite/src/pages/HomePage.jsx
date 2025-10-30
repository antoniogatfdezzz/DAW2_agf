import { Link } from 'react-router-dom'

function HomePage() {
  return (
    <div className="py-5 text-center">
      <h1 className="display-5 fw-bold">Bienvenido a CinemaDAW</h1>
      <p className="lead mt-3 mb-4">Explora, busca y descubre información de tus películas favoritas.</p>
      <Link to="/movies" className="btn btn-primary btn-lg">Ver catálogo</Link>
    </div>
  )
}

export default HomePage
