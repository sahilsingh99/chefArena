import React, {Component} from 'react';
// importing css
import './NavBar.css';
import Cookies from 'universal-cookie';
// import { Redirect } from "react-router-dom";

class NavBar extends Component {
    
    constructor(props) {
        super(props);
        this.state = {
            username : "Guest",
            rating : '',
            redirect : null
        }
        this.login = this.login.bind(this);
    }

    async componentDidMount() {
         const cookie = new Cookies();
         var username = cookie.get('user');
         //console.log("into component");
        if(username === undefined) username = '';
         var url = 'http://backend.test/?username='+username;
         let response = await fetch(url, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            }
         });
         const data = await response.json();
         if(data['data']['username'] != null) {
            if(data['data']['username'] === '') {
                this.setState({
                    username : "Guest",
                    rating : 0
                })
            }
            else {
                this.props.handler(data['data']['access_token']);
                this.setState({
                    username : data['data']['username'],
                    rating : data['data']['band']
                })
            }
         }

         if(this.props.state.isLoggedIn === false) {
            let query = window.location.search.substring(1);
            let auth_code = query.split("&")[0].split("=")[1];
            if (auth_code) {
                var url2 = "http://backend.test/login?code="+auth_code;
                const response = await fetch(url2, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data['status'] === 'OK') {
                const cookies = new Cookies();
                cookies.set('user', data['data']['username'], { path: '/',maxAge: 3600});
                this.props.handler(data['data']['access_token']);
                    this.setState(pstate => {
                    return {
                        username: data['data']['username'],
                        rating: data['data']['band']
                        }
                    });   
                } else{
                    console.log("what's the issue", data);
                    alert('OOpss Something is going wrong .. try again');
                }
            }
        }
    }

    async login() {
        //console.log(this);
        if(this.props.state.isLoggedIn === false) {
            window.location.href = "https://api.codechef.com/oauth/authorize?response_type=code&client_id=1d6ff67cd4f31121f089d15c1cbb93b8&state=xyz&redirect_uri=https://lit-dawn-63895.herokuapp.com/";
        } else {
            const cookie = new Cookies();
            const username = cookie.get('user');
            const url = "http://backend.test/logout?username="+username;
            let response = await fetch(url,{
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                } 
            });
            let data = await response.json();
            if(data['status'] === 'OK') {
                cookie.remove('user');
            }
            this.setState({
                username : "Guest",
                rating : 0
            })
            this.props.handler("");
        }
    }

    render() {
        return(
            <div className = "main">
                <div className = "container">
                    <a className = "user_name" href = "/">{this.state.username}</a>
                    <a className = "logo" href = "/">ChefArena</a>
                    <a className = "btn"   onClick = {this.login}>{this.props.state.isLoggedIn ? "Logout" : "Login"}</a>
                </div>
            </div>   
        );
    }
    
}

export default NavBar;