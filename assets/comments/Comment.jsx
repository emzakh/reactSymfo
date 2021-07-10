import {render, unmountComponentAtNode} from 'react-dom'
import React, {useCallback, useEffect, useRef, useState} from 'react'
import {useFetch, usePaginatedFetch} from "../hooks/hooks"
import {Icon} from '../components/Icon'
import {Field} from "../components/Form";

const dateFormat = {
    dateStyle: 'medium',
    timeStyle: 'short'
}

const VIEW = 'VIEW'
const EDIT = 'EDIT'

function Comments ({post, user}) {
    const {items: comments, setItems:setComments, load, loading, count, hasMore} = usePaginatedFetch('/api/commentaires?recette=' +post)

    const addComment = useCallback(comment => {
        setComments(comments => [comment, ...comments])
    }, [])

    const updateComment = useCallback((newComment, oldComment) => {
        setComments(comments => comments.map(c => c === oldComment ? newComment : c))
    }, [])

    const deleteComment = useCallback(comment => {
        setComments(comments => comments.filter(c => c !== comment))
    }, [])

    useEffect(() => {
        load()
    }, [])

    return <div>
        <Title count={count} />
        {user && <CommentForm post={post} onComment={addComment}/>}
        {comments.map(c =>
            <Comment
                key={c.id}
                comment={c}
                canEdit={c.author.id === user }
                onDelete={deleteComment}
                onUpdate={updateComment}
            />
        )}
        {hasMore && <button disabled={loading} className="btn btn-primary" onClick={load}> Charger plus de commentaires</button>}
    </div>
}

const Comment = React.memo(({comment, onDelete, canEdit, onUpdate}) => {
    const date = new Date(comment.createdAt)

    //Events
    const toggleEdit = useCallback(() =>setState(state=>state===VIEW ? EDIT : VIEW), [])
    const onDeleteCallback = useCallback(()=>onDelete(comment),[comment])
    const onComment = useCallback((newComment)=>{
        onUpdate(newComment, comment)
        toggleEdit()
    }, [comment])

    // Hooks
    const [state, setState] = useState(VIEW)
    const {loading: loadingDelete, load: callDelete} = useFetch(comment['@id'], 'DELETE', onDeleteCallback)

    //Rendu
    return <div className="row post-comment d-flex flex-column">
        <h4 className="col-sm-3 d-flex flex-column">
            <strong>{comment.author.firstName}</strong>
            commenté le
            <strong>{date.toLocaleString(undefined, dateFormat)}</strong>
        </h4>
        <div className="col-sm-9">
            {state === VIEW ?
                <p>{comment.contenu}</p> :
                <CommentForm comment={comment} onComment={onComment} onCancel={toggleEdit}/>
            }

            {(canEdit && state !== EDIT) && <p>
               <button className="btn btn-danger" onClick={callDelete.bind(this, null)} disabled={loadingDelete}>
                   <Icon icon="trash"/> Supprimer
               </button>
                <button className="btn btn-secondary" onClick={toggleEdit}>
                    <Icon icon="pen"/> Editer
                </button>
            </p>}
        </div>

    </div>
})

//je transforme function CommentForm en const CommentForm pour le mémoriser et diminuer le nombre de rendu non necessaire



const CommentForm = React.memo(({post=null, onComment, comment = null, onCancel = null})=>{
    //Variables
    const ref = useRef(null) //permettra lorsqu'on aura besoin d'aller recup la valeur du contenu de faire un ref.current.value
    //console.log(ref) recup le textarea
    const onSuccess = useCallback(comment=>{
        onComment(comment)
        ref.current.value = ''
    },[ref, onComment])

    //Hooks
    const method = comment ? 'PUT' : 'POST'
    const url = comment ? comment['@id'] : '/api/commentaires'
    const{load, loading, errors, clearError} = useFetch(url, method, onSuccess)

    //Méthodes
    const onSubmit = useCallback(e => {
        e.preventDefault()
        load({
            contenu: ref.current.value,
            recette:"/api/recettes/" + post
        })
    }, [load, ref, post])


    //Effets
    useEffect(()=>{
        if(comment && comment.contenu && ref.current){
            ref.current.value = comment.contenu
        }
    }, [comment, ref])


    return <div className="well">
        <form onSubmit={onSubmit}>
            {comment ===null && <fieldset>
                <legend><Icon icon="comment" />Laisser un commentaire</legend>
            </fieldset> }

            <Field name="content" help=" Les commentaires non conformes à notre code de consuite seront modérés."
                   ref={ref}
                   required
                   minLength={2}
                   onChange={clearError.bind(this, 'contenu')}
                   error={errors['contenu']}>
                   Votre commentaire
            </Field>
                <div className="form-group">
                    <button className="btn btn-primary" disabled={loading}>
                        <Icon icon="paper-plane"/> {comment ===null ? 'Envoyer' : 'Editer'}
                    </button>
                    {onCancel && <button className="btn btn-secondary" onClick={onCancel}>
                        Annuler
                    </button>}
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


    constructor (){
        super()
        this.observer = null
    }



    connectedCallback () {
        const post = parseInt(this.dataset.post, 10)
        const user = parseInt(this.dataset.user, 10) || null //si valeur user pas définie on renvoi null pour pas afficher 0
        if(this.observer===null){
            this.observer = new IntersectionObserver((entries, observer) =>{
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.target === this){
                     observer.disconnect()
                     render (<Comments post={post} user={user}/>, this)
                    }
                })
            })
        }
        this.observer.observe(this)
    }

    disconnectedCallback() {
        if(this.observer){
            this.observer?.disconnect()
        }
        unmountComponentAtNode(this)
    }
}

customElements.define('post-comments', CommentsElement)