import RemittanceController from './RemittanceController'
import CartController from './CartController'
import NotificationController from './NotificationController'
import ProfileController from './ProfileController'
import PhoneController from './PhoneController'
import PostController from './PostController'
import OrderController from './OrderController'

const Client = {
    RemittanceController: Object.assign(RemittanceController, RemittanceController),
    CartController: Object.assign(CartController, CartController),
    NotificationController: Object.assign(NotificationController, NotificationController),
    ProfileController: Object.assign(ProfileController, ProfileController),
    PhoneController: Object.assign(PhoneController, PhoneController),
    PostController: Object.assign(PostController, PostController),
    OrderController: Object.assign(OrderController, OrderController),
}

export default Client