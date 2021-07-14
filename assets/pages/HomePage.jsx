import React from 'react';

const HomePage = (props) => {
    return (
        <div className="jumbotron">
            <h1 className="display-3">Hello, world! </h1>
            <p className="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or informaiton.</p>
            <hr className="my-4"/>
            <p>It uses utility classes for typography and spacing to space content out whithin the larger container.</p>
            <p className="lead">
                <a href="#" role="button" className="btn btn-primay btn-lg">Learn More</a>
            </p>
        </div>
    );
}

export default HomePage;