import {render, unmountComponentAtNode} from 'react-dom'
import React, {useEffect} from 'react'
import {usePaginatedFetch} from "../hooks/hooks";

function Comments () {
    const {items: comments, load, loading, count} = usePaginatedFetch('/api/commentaires')


    useEffect(()=> load(),[])

    return <div>
        {loading && 'Chargement...'}
        {JSON.stringify(comments)}
        <Title count={count}/>
        <button onClick={load}>Charger les commentaires</button>
    </div>
}

function Title ({count}){
    return <h3>{count} Commentaire{count>1 ? 's' : ''}</h3>
}



class CommentsElement extends HTMLElement {

    connectedCallback(){
      render (<Comments/>, this)
    }

    disconnectedCallback () {
        unmountComponentAtNode(this)
    }

}



customElements.define('post-comments', CommentsElement)