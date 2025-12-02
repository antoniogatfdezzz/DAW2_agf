document.addEventListener('DOMContentLoaded', () => {
  if (typeof initBasicTableSearch === 'function') {
    initBasicTableSearch({ usuarios: {
      searchInputId: 'usuariosSearchInput',
      clearBtnId: 'usuariosSearchClear',
      searchInfoId: 'usuariosSearchInfo',
      searchResultsId: 'usuariosSearchResults',
      totalCountId: 'total-count',
      tableSelector: '#tabla-usuarios tbody tr',
      columnsCount: 4
    }});
  } else {
    console.warn('initBasicTableSearch no disponible');
  }
});