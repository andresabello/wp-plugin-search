import axios from "axios"

document.addEventListener("DOMContentLoaded", () => {
    console.log(ajax_object)
    document
        .getElementById("pi-ingest-button")
        .addEventListener("click", (event) => {
            event.preventDefault()            

            let formData = new FormData()
            formData.append("action", "send_data_to_search_service")
            formData.append("nonce", ajax_object.ajax_nonce)

            axios
                .post(ajax_object.ajax_url, formData)
                .then((data) => {
                    console.log(data)
                })
                .catch((error) => {
                    console.log(error)        
                })
        })
})
