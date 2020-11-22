import React, {Component} from 'react';
// importing css
import './TagList.css';

class TagList extends Component {

    constructor(props) {
        super(props);
        this.state = {
            tags : [],
            author : []
        };
    }

    componentDidMount() {
        let collectAll = this.props.items;
        console.log("all elements in taglist", this.props.items);
        this.setState({tags : collectAll});
    }

    renderList() {
        const tags = this.props.items;
        if(tags.length === 0) {
            return null;
        }
        return (
            <ul className = "leftlist">
                {tags.map((item, i) => <li className = "leftlistelement" key = {i}>{item}</li>)}
            </ul>
        )
    }

    render() {
        return (
            <div className = "list">
                <h1>ALL Tags</h1>
                {this.renderList()}
            </div>
        )
    }
}

export default TagList;