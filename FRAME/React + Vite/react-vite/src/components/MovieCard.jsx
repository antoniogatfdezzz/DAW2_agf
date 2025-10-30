import { Link } from 'react-router-dom'

function MovieCard({ movie }) {
  return (
    <div className="card h-100 shadow-sm">
      {movie.poster_url && (
        <img src={movie.poster_url} className="card-img-top" alt={movie.title} />
      )}
      <div className="card-body d-flex flex-column">
        <h5 className="card-title">{movie.title}</h5>
        <p className="card-text mb-1"><strong>Año:</strong> {movie.year}</p>
        <p className="card-text mb-1"><strong>Género:</strong> {movie.genre}</p>
        <p className="card-text text-truncate">{movie.description}</p>
        <div className="mt-auto">
          <Link to={`/movies/${movie.id}`} className="btn btn-outline-primary w-100">Ver detalle</Link>
        </div>
      </div>
    </div>
  )
}

export default MovieCard
