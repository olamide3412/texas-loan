// resources/js/fontawesome.js
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
  faUserSecret, faUser, faSearch, faCircle, faFileCirclePlus, faGaugeHigh, faEye,
  faPhone, faCertificate, faXmark, faWifi, faClock, faShield, faThumbsUp, faShieldAlt,
  faCoins, faUsd, faAngleRight, faMapMarkerAlt, faEnvelope, faGift, faBolt, faCheckCircle,
  faBookOpen, faFileAlt, faBook, faEdit, faComment, faGlobe, faComments, faTruck, faLeaf,
  faHandHoldingUsd, faTractor, faCow, faSeedling, faShoppingBasket, faCreditCard, faMotorcycle,
  faCar, faBox, faLaptop, faBatteryFull, faHouse
} from '@fortawesome/free-solid-svg-icons'

import {
  faBitcoin, faEthereum, faFacebook, faInstagram, faTwitter, faXTwitter, faWhatsapp
} from '@fortawesome/free-brands-svg-icons'

library.add(
  faUserSecret, faUser, faSearch, faCircle, faFileCirclePlus, faGaugeHigh, faEye, faPhone,
  faCertificate, faXmark, faWifi, faClock, faShield, faThumbsUp, faShieldAlt, faCoins, faBitcoin,
  faEthereum, faUsd, faFacebook, faInstagram, faTwitter, faXTwitter, faAngleRight, faMapMarkerAlt,
  faEnvelope, faWhatsapp, faGift, faBolt, faCheckCircle, faBookOpen, faFileAlt, faBook, faEdit,
  faComment, faGlobe, faComments, faTruck, faLeaf, faHandHoldingUsd, faTractor, faCow, faSeedling,
  faShoppingBasket, faCreditCard, faMotorcycle, faCar, faBox, faLaptop, faBatteryFull, faHouse
)

export { FontAwesomeIcon }
