import React from 'react';
import ReactDOM from 'react-dom';
import {
    BrowserRouter as Router,
    Switch,
    Route
  } from "react-router-dom";

export default function Index() {
    return (
        <Router>
            <Switch>
                <Route exact path="/">
                    <Home />
                </Route>
                <Route path="/about">
                    <About />
                </Route>
            </Switch>
        </Router>
    );
}
function Home()
{
    return(
        <h1>Home</h1>
    )
}
function About()
{
    return(
        <h1>About</h1>
    )
}
ReactDOM.render(<Index />, document.getElementById('root'));

