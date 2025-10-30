import { useMemo, useState } from 'react'
import SearchBar from '../components/SearchBar'
import FilterBar from '../components/FilterBar'
import MovieList from '../components/MovieList'
import mockMovies from '../data/mockMovies'

function MoviesPage() {
  const [query, setQuery] = useState('')
  const [genre, setGenre] = useState('')
  const [year, setYear] = useState('')

  const genres = useMemo(
    () => Array.from(new Set(mockMovies.map((m) => m.genre))).sort(),
    []
  )
  const years = useMemo(
    () => Array.from(new Set(mockMovies.map((m) => m.year))).sort((a, b) => b - a),
    []
  )

  const filtered = useMemo(() => {
    return mockMovies.filter((m) => {
      const matchesQuery = m.title.toLowerCase().includes(query.toLowerCase())
      const matchesGenre = genre ? m.genre === genre : true
      const matchesYear = year ? String(m.year) === String(year) : true
      return matchesQuery && matchesGenre && matchesYear
    })
  }, [query, genre, year])

  const handleFilters = ({ genre: g, year: y }) => {
    if (g !== undefined) setGenre(g)
    if (y !== undefined) setYear(y)
  }

  return (
    <div className="vstack gap-3">
      <div className="row g-2 align-items-stretch">
        <div className="col-12 col-lg-6">
          <SearchBar onSearch={setQuery} />
        </div>
        <div className="col-12 col-lg-6">
          <FilterBar
            genres={genres}
            years={years}
            selectedGenre={genre}
            selectedYear={year}
            onChange={handleFilters}
          />
        </div>
      </div>
      <MovieList movies={filtered} />
    </div>
  )
}

export default MoviesPage
