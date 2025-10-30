import MovieCard from './MovieCard'

function MovieList({ movies = [] }) {
  if (!movies.length) {
    return <p className="text-center text-muted">No se encontraron películas.</p>
  }
  return (
    <div className="row g-3">
      {movies.map((m) => (
        <div key={m.id} className="col-12 col-sm-6 col-md-4 col-lg-3">
          <MovieCard movie={m} />
        </div>
      ))}
    </div>
  )
}

export default MovieList
