
export { get, post, del }

/**
 * Implementación de método GET
 */
function get(url) {
    return fetch(url);
}

/**
 * Implementación de método POST
 */
function post(url, objeto) {
    return fetch(
        url,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(objeto)
        }
    );
}


/**
 * Implementación de método DELETE
 */
function del(url, id) {
    return fetch(
        url+"/"+id, 
        {
            method: "DELETE"
        }
    );
}
