import PostController from './PostController'
import GeneralController from './GeneralController'
import Client from './Client'
import CartController from './CartController'
import Admin from './Admin'
import Auth from './Auth'

const Controllers = {
    PostController: Object.assign(PostController, PostController),
    GeneralController: Object.assign(GeneralController, GeneralController),
    Client: Object.assign(Client, Client),
    CartController: Object.assign(CartController, CartController),
    Admin: Object.assign(Admin, Admin),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers