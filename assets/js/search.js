import "./../scss/search.scss"
import axios from "axios"

const removeHtmlTags = (inputString)  => {
    return inputString.replace(/<[^>]*>/g, '');
}

const inputElement = document.getElementById("pi-search")
const dropdownContainer = document.getElementById("pi-search-items")
const loaderElement = document.getElementById('loader');

let busy = false

// Event listener for input changes
inputElement.addEventListener("input", (e) => {
    e.preventDefault()

    // Clear previous suggestions
    dropdownContainer.style.display = 'none'
    dropdownContainer.innerHTML = ""
    
    const inputValue = e.target.value.toLowerCase()
    if (inputValue.length <= 3) {
        return
    }

    if (busy) {
        return
    }

    let formData = new FormData()
    formData.append("action", "pi_search_posts")
    formData.append("nonce", ajax_object.nonce)
    formData.append("query", inputValue)

    busy = true
    loaderElement.style.display = 'block';

    axios
        .post(ajax_object.ajax_url, formData)
        .then(({ data }) => {
            let filteredPosts = data.data.posts
            dropdownContainer.style.display = 'block'
            loaderElement.style.display = 'none';
            // Display suggestions
            filteredPosts.forEach((post) => {
                const item = document.createElement("div")
                const title = document.createElement("p")
                const content = document.createElement("p")
                const linkElement = document.createElement('a');
                const contentLinkElement = document.createElement('a');

                item.classList.add("pi-autocomplete-item")
                title.classList.add("pi-search-title")
                content.classList.add("pi-search-content")

                title.innerHTML = post.title.String
                content.innerHTML = removeHtmlTags(post.content.String).substring(0, 100) + '...'
                linkElement.href = post.link;
                contentLinkElement.href = post.link;

                linkElement.appendChild(title)
                contentLinkElement.appendChild(content)                
                item.appendChild(linkElement)
                item.appendChild(contentLinkElement)

                // Add click event to populate input
                item.addEventListener("click", function () {
                    inputElement.value = post.title.Stringg
                    // Clear suggestions after selection
                    dropdownContainer.innerHTML = ''
                    dropdownContainer.style.display = 'none'
                })
                dropdownContainer.appendChild(item)
            })

            busy = false 
        })
        .catch((error) => {
            //TODO: Handle the error
            console.log(error)
            busy = false
            loaderElement.style.display = 'none';
        })
})

// Event listener for clicks on the document
document.addEventListener('click', (event) => {
    const isClickInsideDiv = dropdownContainer.contains(event.target)

    if (!isClickInsideDiv) {
        dropdownContainer.style.display = 'none'
    }
});

// Prevent the click event from propagating inside the div
dropdownContainer.addEventListener('click', (event) => {
    event.stopPropagation()
})

// Close the dropdown when clicking outside of it
document.addEventListener("click", (event) => {
    if (event.target !== inputElement && event.target !== dropdownContainer) {
        dropdownContainer.innerHTML = ""
    }
})