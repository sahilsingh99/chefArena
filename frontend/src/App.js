import './App.css';
import React from 'react'
// importing NavBar
import NavBar from './components/NavBar';
import SearchBar from './components/SearchBar';
// import TagList from './components/TagList';
// import Problems from './components/Problems';

class App extends React.Component {

  constructor() {
    super();
    this.state = {
      isLoggedIn : false,
      accessToken : ''
      //allowProblems : false
    }
    // this.tags = {};
    // this.problems = [];
    this.loginStateHandler = this.loginStateHandler.bind(this);
    // this.loadTags = this.loadTags.bind(this);
    // this.loadProblems = this.loadProblems.bind(this);
  }

  loginStateHandler(accessT) {
      if(this.state.isLoggedIn) {
        this.setState({
          accessToken : '',
          isLoggedIn : false
        })
      }
      else {
        this.setState({
          accessToken : accessT,
          isLoggedIn : true
        })
      }
  }
  
  // loadTags(tags) {
  //   //console.log("tags in load Tags");
  //   //console.log(tags);
  //   this.tags = tags;
  //   this.setState({
  //     allowProblems : false
  //   });
  // }

  // loadProblems(problems) {
  //   this.problems = problems;
  //   this.setState({
  //     allowProblems : true
  //   });
  // }

  
  render() {
    console.log(this.state.accessToken);
    return (
      <div className="App">
        <NavBar 
          state = {this.state}
          handler = {this.loginStateHandler}  
          />
          <div className = "App-Component">
              <SearchBar 
                state = {this.state}
                // tagHandler = {this.loadTags}
                // problemHandler = {this.loadProblems}
              />
          </div>
          {/* {this.state.allowProblems ? 
                 <Problems state = {this.state} problems = {this.problems} /> :
                <TagList state = {this.state} tags = {this.tags} /> 
              } */}
      </div>
    );
  }
}

export default App;