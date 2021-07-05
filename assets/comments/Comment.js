class CommentsElement extends HTMLElement {
    connectedCallback(){
        this.innerHTML = 'Bonjour tout le monde'
    }
}

customElements.define('post-comments', CommentsElement)