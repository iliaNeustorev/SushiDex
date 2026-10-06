import CartController from './CartController'
import ProfileController from './ProfileController'
import PhoneController from './PhoneController'
import OrderController from './OrderController'
import RemittanceController from './RemittanceController'

const Client = {
    CartController: Object.assign(CartController, CartController),
    ProfileController: Object.assign(ProfileController, ProfileController),
    PhoneController: Object.assign(PhoneController, PhoneController),
    OrderController: Object.assign(OrderController, OrderController),
    RemittanceController: Object.assign(RemittanceController, RemittanceController),
}

export default Client