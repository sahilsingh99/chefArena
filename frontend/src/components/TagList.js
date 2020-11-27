import React, {Component} from 'react';
// importing css
import './TagList.css';

class TagList extends Component {

    constructor(props) {
        super(props);
        this.state = {};
    }

    renderList() {
        const tags = this.props.tags;
        const tagArray = Object.values(tags);
        //console.log("tags inside render of taglist ", tags);
        if(tags.length === 0) {
            return null;
        }
        return (
            <ul className = "flexTagList">
                {
                    tagArray.map(
                        (item,i) => 
                        <div className = "tagCard" onClick = {this.props.handler(item.tag)} key = {i}>
                            {item.tag} <i className="fa fa-close icon"></i> {item.count}
                        </div>
                    )
                }
            </ul>
        )
    }

    render() {
        return (<div>
            <div className = "list">
                <h1>ALL TAGS</h1>
                {this.renderList()}
            </div>
        </div>
            
        )
    }
}

export default TagList;