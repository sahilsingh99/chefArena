import React, {Component} from 'react';
// importing css
import './SearchBar.css';

 
class SearchBar extends Component {
    constructor(props) {
        super(props);
        
        this.state = {
            suggestions : [],
            text : '',
            selectedTags : []
        }
    }

    // function for handling events in searchbas
    onTextChanged = (e) => {
        e.preventDefault();
        let value = e.target.value;
        let suggestions = [];
        if (value.length > 0&&value.charAt(value.length-1)!=='\\') {
            const regex = new RegExp(`^${value}`,'i');
            suggestions = this.props.items.sort().filter(v => regex.test(v));
        }
        this.setState(() => ({ suggestions,text : value }));
    }

    // function for adding tags
    addSelected = (tag) => (e) => {
        let {selectedTags} = this.state;
            // insert only tags which are not already in selected tags
            if(!selectedTags.includes(tag)) {
                   selectedTags.push(tag);
                }
            this.setState( () => {
                return {
                    text : '',
                    suggestions : [],
                    selectedTags: selectedTags,
                }
            })
        // console.log(this.state.selectedTags);
    }

    // function for deleting tags
    deleteTag(tag) {
        let {selectedTags} = this.state;
        let updatedtags = selectedTags.filter(item => item !== tag)
        console.log(updatedtags);
        this.setState( () => {
            return {
                selectedTags: updatedtags,
            }
        })
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
            </div>
        )
    }
}




export default SearchBar;