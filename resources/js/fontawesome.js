// fontawesome.js
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faUsers, 
    faBuilding, 
    faUserGraduate, 
    faBriefcase, 
    faUserPlus,
    faFileAlt,
    faChartBar,
    faComments,
    faCog,
    faExclamationCircle
} from '@fortawesome/free-solid-svg-icons';

// Add icons to the library
library.add(
    faUsers, 
    faBuilding, 
    faUserGraduate, 
    faBriefcase, 
    faUserPlus,
    faFileAlt,
    faChartBar,
    faComments,
    faCog,
    faExclamationCircle
);

export { FontAwesomeIcon }; 