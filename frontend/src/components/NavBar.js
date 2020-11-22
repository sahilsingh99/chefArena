import React, {Component} from 'react';
// importing css
import './NavBar.css';

class NavBar extends Component {
    
    constructor(props) {
        super(props);
        this.state = {}
    }

    render() {
        return(
            <div className = "main">
                <div className = "container">
                    <a className = "user_name" href = "#">{this.props.name}</a>
                    <a className = "logo" href = "#">ChefArena</a>
                    <a className = "btn" href = "#">{this.props.isLoggedIn ? "Logout" : "Login"}</a>
                </div>
            </div>   
        );
    }
    
}

export default NavBar;