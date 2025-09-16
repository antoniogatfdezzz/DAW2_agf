SELECT p.nombre
FROM PROFESORES p
JOIN IMPARTE imp ON p.cod_prof = imp.cod_prof
JOIN CENTROS centr ON imp.cod_centro = centr.cod_centro
LEFT JOIN PRIVADO priv ON centr.cod_centro = priv.cod_centro
LEFT JOIN CONCERTADO con ON centr.cod_centro = con.cod_centro
WHERE p.especialidad = 'informatica'
  AND imp.turno = 'tarde'
  AND priv.cod_centro IS NULL
  AND con.cod_centro IS NULL;
