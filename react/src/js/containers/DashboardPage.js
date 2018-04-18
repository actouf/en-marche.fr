import React, { Component } from 'react';

import AdherentContainer from './AdherentContainer';
import CommitteeContainer from './CommitteeContainer';
import EventContainer from './EventContainer';

class DashboardPage extends Component {
    render() {
        return (
            <div className="dashboard__ctn">
                <div className="wrapper">
                    <AdherentContainer />
                    <CommitteeContainer />
                    <EventContainer />
                </div>
            </div>
        );
    }
}

export default DashboardPage;
