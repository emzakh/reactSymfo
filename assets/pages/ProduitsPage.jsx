import React, { Fragment, useEffect, useState } from 'react';
import axios from "axios"

const ProduitsPage = (props) => {
const [produits, setProduits] = useState([]);
useEffect(() => {
    axios.get("http://localhost:8000/api/produits")
        .then(response => response.data['hydra:member'])
        .then(data => setProduits(data))
        .catch(error => console.log(error.response))
}, [])
return (    

    <h1>SALUT !</h1>

    //    produits.map(produit => (
    //         <tr key={produit.id}>
    //             <td>{produit.id}</td>
    //             <td>{produit.nom}</td>
    //         </tr>
    //     ))
    
);
}

export default ProduitsPage;