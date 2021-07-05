import {render} from 'react-dom'
import React from 'react'
import {usePaginatedFetch} from "../hooks/hooks";

function Comments () {
    const {items: comments, load, loading} = usePaginatedFetch('/api/commentaires')

    return <div>
        {loading && 'Chargements...'}
        {JSON.stringify(comments)}
        <button onClick={load}>Charger les commentaires</button>


    </div>


}

class CommentsElement extends HTMLElement {
    connectedCallback () {
        render (<Comments/>, this)
    }
}

customElements.define('post-comments', CommentsElement)