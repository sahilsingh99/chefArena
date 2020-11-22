import './App.css';
import React from 'react'
// importing NavBar
import NavBar from './components/NavBar';
import SearchBar from './components/SearchBar';
import TagList from './components/TagList';

class App extends React.Component {

  constructor() {
    super();
    this.state = {
      allTags : [],
      isLoggedIn : false,
      user : 'Guest'
    }
  }

  async componentDidMount() {
    fetch('http://backend.test/',{
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
        }
      })
          .then(response => response.json())
          .then(data => {
            //console.log(data);
            let items = data['tags'].map(item => item);
            console.log("all tags" , items);
            this.setState({
              allTags : items
            })
          })
          .catch(err => {
            console.log(err);
          }) 
  }

  render() {
    return (
      <div className="App">
        <NavBar 
          name = {this.state.user} 
          isLoggedIn = {this.state.isLoggedIn} />
        <div className = "main-content">
            <div className = "App-Component">
              <SearchBar items = {this.state.allTags} />
            </div>
            <div className = "tag-list">
              <TagList items = {this.state.allTags} />
            </div>
        </div>
      </div>
    );
  }
}

export default App;