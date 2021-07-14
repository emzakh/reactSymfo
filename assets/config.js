 //export const API_URL = "http://localhost:8000/api/"
 // puis on va dans webpack.config.js :
 
 export const API_URL = process.env.API_URL

 export const PRODUITS_API = API_URL + "produits"
 export const RECETTES_API = API_URL + "recettes"
 export const USERS_API = API_URL + "users"
 export const LOGIN_API = API_URL + "login_check"
 