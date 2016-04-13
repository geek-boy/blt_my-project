function getDeviceType() {
    "use strict";
    var Breakpoints = window.breakpointSettings.Breakpoints;
    var DeviceMapping = window.breakpointSettings.DeviceMapping;

    if (window.innerWidth <= Breakpoints[DeviceMapping.tablet]) {
        return 'smartphone';
    }

    if (window.innerWidth <= Breakpoints[DeviceMapping.desktop]) {
        return 'tablet';
    }

    return 'desktop';
}

function deviceIsMobile() {
    "use strict";
    return (getDeviceType() === 'smartphone');
}

function deviceIsTablet() {
    "use strict";
    return (getDeviceType() === 'tablet');
}

function deviceIsDesktop() {
    "use strict";
    return (getDeviceType() === 'desktop');
}

