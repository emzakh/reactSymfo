import React,{useState} from 'react'
import ReactDom from 'react-dom'
import { HashRouter, Switch, Route, withRouter } from 'react-router-dom'
//import { BrowserRouter as Router, Link, Switch, Route } from 'react-router-dom';
import Navbar from './components/Navbar'
import HomePage from './pages/HomePage'
import ProduitsPage from './pages/ProduitsPage'
import PrivateRoute from './components/PrivateRoute'
import AuthContext from './contexts/AuthContext'
import LoginPage from './pages/LoginPage'
import './styles/app.css';
import './bootstrap';
import './comments/Comment.jsx';
import authAPI from './services/authAPI';
import { ToastContainer, toast } from 'react-toastify';


const App = () => {

    const [isAuthenticated, setIsAuthenticated] = useState(authAPI.isAuthenticated)

    const NavbarWithRouter = withRouter(Navbar)

    const contextValue = {
        isAuthenticated: isAuthenticated,
        setIsAuthenticated: setIsAuthenticated
    }

    return (
        <AuthContext.Provider value={contextValue}>
        <HashRouter>   
        <NavbarWithRouter />      
        <main className="container pt-5">           
                    <Switch>
                        <Route path="/login" component={LoginPage}></Route>    
                        <Route path="/produits" component={ProduitsPage}></Route>     
                        <Route path="/" component={HomePage}> </Route>
                    </Switch>     
        </main>        
        </HashRouter>
       </AuthContext.Provider>
    )
}

const rootElement = document.querySelector('#app')
ReactDom.render(<App />, rootElement)