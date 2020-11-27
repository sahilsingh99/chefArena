import React, {Component} from 'react';
// importing css
import './Problems.css';

class Problems extends Component {
    constructor(props) {
        super(props)
        this.state = {}
    }
    renderProblemList() {
        const problems = this.props.problems;
        const problemArray = Object.values(problems);
        console.log("tags inside render of problemlist ", problems);
        if(problems.status === "NOT FOUND") {
            return (
                <div className = "notFound">
                    NO Problems Found :(
                </div>
            );
        }
        return (
            <ul className = "flexProblemList">
                {
                    problemArray.map(
                        (item,i) => 
                        <div className = "problemCard" key = {i}>
                            <ul className = "pul">
                                <li className = "pli">
                                    Code : {item.code}
                                </li>
                                <li className = "pli">
                                    Author : {item.author}
                                </li>
                                <li className = "pli">
                                    Attempted : {item.attempted}
                                </li>
                                <li className = "pli">
                                    Solved : {item.solved}
                                </li>
                                <li className = "pli add" >
                                    ADD TAGS
                                </li>
                            </ul> 
                            <ul className = "pul">
                                {item.tags.map((tag,i) => <li key = {i} onClick = {this.props.handler(tag)} className = "pli">{tag}</li>)}
                            </ul>
                            <br/>
                        </div>
                    )
                }
            </ul>
        )
    }

    render() {
        return (
           <div className = "ProblemList">
               {this.renderProblemList()}
           </div>
        )
    }
}

export default Problems;