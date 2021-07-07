import React from 'react';



export default Icon;

export function Icon ({icon}) {
    return <i className={"fa fa-" + icon} aria-hidden='true'></i>
}