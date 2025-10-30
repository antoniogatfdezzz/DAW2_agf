import { useState } from 'react'

function SearchBar({ onSearch, placeholder = 'Buscar por título…', defaultValue = '' }) {
  const [query, setQuery] = useState(defaultValue)

  const submit = (e) => {
    e.preventDefault()
    onSearch?.(query)
  }

  return (
    <form className="input-group" onSubmit={submit} role="search">
      <input
        type="search"
        className="form-control"
        value={query}
        onChange={(e) => setQuery(e.target.value)}
        placeholder={placeholder}
        aria-label="Buscar"
      />
      <button className="btn btn-primary" type="submit">Buscar</button>
    </form>
  )
}

export default SearchBar
