import {render} from 'react-dom'
import React, {useEffect, useRef, useState} from 'react'
import {usePaginatedFetch} from "../hooks/hooks"
import {Icon} from '../components/Icon'
import {Field} from "../components/Form";
const dateFormat = {
    dateStyle: 'medium',
    timeStyle: 'short'
}
function Comments ({post, user}) {
    const {items: comments, load, loading, count, hasMore} = usePaginatedFetch('/api/commentaires?recette=' +post)

    useEffect(() => {
        load()
    }, [])

    return <div>
        <Title count={count} />
        {user && <CommentForm post={post} />}
        {comments.map(comment => <Comment key={comment.id} comment={comment} />)}
        {hasMore && <button disabled={loading} className="btn btn-primary" onClick={load}> Charger plus de commentaires</button>}
    </div>
}

const Comment = React.memo(({comment}) => {
    const date = new Date(comment.createdAt)
    return <div className="row post-comment d-flex flex-column">
        <h4 className="col-sm-3 d-flex flex-column">
            <strong>{comment.author.firstName}</strong>
            commenté le
            <strong>{date.toLocaleString(undefined, dateFormat)}</strong>
        </h4>
        <div className="col-sm-9">
            <p>{comment.contenu}</p>
        </div>

    </div>
})

//je transforme function CommentForm en const CommentForm pour le mémoriser et diminuer le nombre de rendu non necessaire



const CommentForm = React.memo(({post})=>{

    const ref = useRef(null) //permettra lorsqu'on aura besoin d'aller recup la valeur du contenu de faire un ref.current.value
    //console.log(ref) recup le textarea

    return <div className="well">
        <form>
            <fieldset>
                <legend><Icon icon="comment" />Laisser un commentaire</legend>
            </fieldset>
            <Field name="content" help=" Les commentaires non conformes à notre code de consuite seront modérés." ref={ref} error="Votre commentaire est trop court">Votre commentaire</Field>
                <div className="form-group">
                    <button className="btn btn-primary">
                        <Icon icon="paper-plane"/> Envoyer
                    </button>
                </div>
        </form>
    </div>
})


function Title ({count}) {
    return <h3>
        <Icon icon="comments"/>
        {count} Commentaire{count > 1 ? 's': ''}
    </h3>
}



class CommentsElement extends HTMLElement {
    connectedCallback () {
        const post = parseInt(this.dataset.post, 10)

        const user = parseInt(this.dataset.user, 10) || null //si valeur user pas définie on renvoi null pour pas afficher 0
        render (<Comments post={post} user={user}/>, this)
    }

    disconnectedCallback() {
        unmountComponentAtNode(this)
    }
}

customElements.define('post-comments', CommentsElement)