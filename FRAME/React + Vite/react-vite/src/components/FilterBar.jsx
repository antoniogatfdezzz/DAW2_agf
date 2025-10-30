function FilterBar({ genres = [], years = [], selectedGenre = '', selectedYear = '', onChange }) {
  const handleChange = (e) => {
    const { name, value } = e.target
    onChange?.({ [name]: value })
  }

  return (
    <div className="row g-2">
      <div className="col-12 col-md-6">
        <select
          className="form-select"
          name="genre"
          value={selectedGenre}
          onChange={handleChange}
        >
          <option value="">Todos los géneros</option>
          {genres.map((g) => (
            <option key={g} value={g}>{g}</option>
          ))}
        </select>
      </div>
      <div className="col-12 col-md-6">
        <select
          className="form-select"
          name="year"
          value={selectedYear}
          onChange={handleChange}
        >
          <option value="">Todos los años</option>
          {years.map((y) => (
            <option key={y} value={y}>{y}</option>
          ))}
        </select>
      </div>
    </div>
  )
}

export default FilterBar
