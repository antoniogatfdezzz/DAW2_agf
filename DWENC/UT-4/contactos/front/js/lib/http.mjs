
export { get, post, put, del }

/**
 * Implementación de método GET
 */
function get(url) {
    return fetch(
        url, 
        {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer "+localStorage.getItem("jwtToken")
            },            
        }

    );
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
                "Authorization": "Bearer "+localStorage.getItem("jwtToken")
            },
            body: JSON.stringify(objeto)
        }
    );
}

/**
 * Implementación de método PUT
 */
function put(url, objeto) {
    return fetch(
        url,
        {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer "+localStorage.getItem("jwtToken")
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
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer "+localStorage.getItem("jwtToken")
            },
        }
    );
}

