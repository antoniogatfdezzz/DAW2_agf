import { useParams, Link } from 'react-router-dom'
import mockMovies from '../data/mockMovies'

function MovieDetailPage() {
  const { id } = useParams()
  const movie = mockMovies.find((m) => String(m.id) === String(id))

  if (!movie) {
    return (
      <div className="text-center">
        <h2 className="mb-3">Película no encontrada</h2>
        <Link to="/movies" className="btn btn-secondary">Volver al listado</Link>
      </div>
    )
  }

  return (
    <div className="row g-4">
      <div className="col-12 col-md-4">
        {movie.poster_url && (
          <img src={movie.poster_url} alt={movie.title} className="img-fluid rounded shadow-sm" />
        )}
      </div>
      <div className="col-12 col-md-8">
        <h2>{movie.title}</h2>
        <p className="mb-1"><strong>Director:</strong> {movie.director}</p>
        <p className="mb-1"><strong>Año:</strong> {movie.year}</p>
        <p className="mb-1"><strong>Género:</strong> {movie.genre}</p>
        <p className="mb-1"><strong>Valoración:</strong> {movie.rating}</p>
        <p className="mt-3">{movie.description}</p>
        <Link to="/movies" className="btn btn-outline-primary mt-2">← Volver</Link>
      </div>
    </div>
  )
}

export default MovieDetailPage
