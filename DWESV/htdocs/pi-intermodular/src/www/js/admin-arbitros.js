document.addEventListener('DOMContentLoaded', () => {
  if (typeof initBasicTableSearch === 'function') {
    initBasicTableSearch({ arbitros: {
      searchInputId: 'arbitrosSearchInput',
      clearBtnId: 'arbitrosSearchClear',
      searchInfoId: 'arbitrosSearchInfo',
      searchResultsId: 'arbitrosSearchResults',
      totalCountId: 'total-count',
      tableSelector: '#tabla-arbitros tbody tr',
      columnsCount: 5
    }});
  } else {
    console.warn('initBasicTableSearch no disponible');
  }
});