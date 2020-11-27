import React, {Component} from 'react';
// importing css
import './SearchBar.css';
import TagList from './TagList';
import Problems from './Problems';
 
class SearchBar extends Component {
    constructor(props) {
        super(props);
        this.items = [];
        this.state = {
            suggestions : [],
            text : '',
            selectedTags : JSON.parse(localStorage.getItem('selectedTags')) || [],
            allowProblems : false,
            addUserDefinedTag : false
        }
        this.tags = {};
        this.problems = {};
        this.updatePage = this.updatePage.bind(this);
        this.getTags = this.getTags.bind(this);
        // this.addToSessionStorage = this.addToSessionStorage.bind(this);
    }

    // addToSessionStorage() {
    //     window.sessionStorage.setItem('selectedTags',this.state.selectedTags);
    // }

     componentDidMount() {
         const retrieveData = JSON.parse(localStorage.getItem('selectedTags')) || [];
         this.setState({selectedTags : retrieveData});
         this.getTags();
     }

     async getTags() {
        var url = "http://backend.test/tags";
        let response = await fetch(url, {
            method : 'GET',
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        let data = await response.json();
        if(typeof data === 'undefined') {
            alert('network error , please refresh :)');
        }
        for(var key in data){
            this.items.push(data[key].tag);
            this.tags[key] = data[key];
        }
        console.log('inside getTags');
        console.log(this.tags);
        this.updatePage();
        //console.log("tags inside searchbar", this.tags);
     }

     async getProblems() {
        var filters = this.state.selectedTags.toString();
        var url = "http://backend.test/tags/problems?filters="+filters;
        let response = await fetch(url, {
            method : 'GET',
            headers : {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        let data = await response.json();
        if(typeof data === 'undefined') {
            alert('network error , please refresh :)');
        }
        for (var member in this.problems) delete this.problems[member];
        for(var key in data){
            this.problems[key] = data[key];
        }
        console.log("problems ",this.problems);
        this.setState({allowProblems : true});
     }

     updatePage() {
         //console.log("length", this.state.selectedTags.length , this.state.selectedTags);
         if(this.state.selectedTags.length) {
             this.getProblems();
             //this.props.problemHandler(this.tags);
         } else {
            for (var member in this.problems) delete this.problems[member];
            //console.log("after deletion ", this.problems);
             //console.log("calling tag loader", this.tags);
             this.setState({allowProblems : false});
             //this.props.tagHandler(this.tags);
         }
     }

    // function for handling events in searchbas
    onTextChanged = (e) => {
        e.preventDefault();
        let value = e.target.value;
        let suggestions = [];
        if(this.items && value !== '' ){
            suggestions = this.items.filter(item => {
            if(item.toLowerCase().includes(value.toLowerCase())){
                return item;
            }
        });}
        this.setState({ suggestions,text : value });
    }

    // function for adding tags
    addSelected = (tag) => (e) => {
        let {selectedTags} = this.state;
            // insert only tags which are not already in selected tags
            if(!selectedTags.includes(tag)) {
                   selectedTags.push(tag);
                }
            this.setState({
                    text : '',
                    suggestions : [],
                    selectedTags: selectedTags,
                }, () => {
                    localStorage.removeItem('selectedTags');
                    localStorage.setItem('selectedTags', JSON.stringify(selectedTags));
                    this.updatePage();
                });
            
        // console.log(this.state.selectedTags);
    }

    // function for deleting tags
    deleteTag(tag) {
        let {selectedTags} = this.state;
        let updatedTags = selectedTags.filter(item => item !== tag)
        //console.log(updatedTags);
        this.setState( {
                selectedTags: updatedTags,
            }, () => {
                localStorage.removeItem('selectedTags');
                localStorage.setItem('selectedTags', JSON.stringify(updatedTags));
                this.updatePage();
            });
            
    }

    // functionf or suggestions
    renderSuggestion() {
        const {suggestions} = this.state;
        if(suggestions.length === 0) {
            return null;
        }
        return (
            <ul>
                {suggestions.map((item, i) => <li onClick = {this.addSelected(item)} key = {i}>{item}</li>)}
            </ul>
        )
    }

    showSelectedTags() {
        const {selectedTags} = this.state;
        if(selectedTags.length === 0) {
            //console.log("00");
            return null;
        }
        return (
            <ul className = "taglist">
                {selectedTags.map((item, i) => <li className = "tagelement" onClick={() => this.deleteTag(item)} key = {i}>
                    {item}
                    <i className="fa fa-close icon"></i></li>)}
            </ul>
        )
    }

    render() {
        let {text} = this.state;
        return (
            <div className = "main2">
                    {/* show selected Tags  */}
                    {this.showSelectedTags()}
                <div className = "searchbar">
                    {/* searchbar snippet */}
                    <input 
                        value = {text} 
                        onChange = {this.onTextChanged} 
                        placeholder = "Search Tag Here..."
                        type = "search"
                        id = "tag"
                        autoComplete = "off"
                    />
                    {/* show suggestions or autoComplete */}
                    {this.renderSuggestion()}
                </div>
                {this.state.allowProblems ? 
                 <Problems state = {this.state} handler = {this.addSelected} problems = {this.problems} /> :
                <TagList state = {this.state} tags = {this.tags} handler = {this.addSelected}/> 
              }
            </div>
        )
    }
}




export default SearchBar;