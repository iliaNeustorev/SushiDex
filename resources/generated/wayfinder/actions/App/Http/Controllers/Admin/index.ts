import OrderController from './OrderController'
import UserController from './UserController'
import PostController from './PostController'
import ImagesController from './ImagesController'
import DashboardController from './DashboardController'
import ProductController from './ProductController'
import TagController from './TagController'
import CategoryController from './CategoryController'
import Trash from './Trash'

const Admin = {
    OrderController: Object.assign(OrderController, OrderController),
    UserController: Object.assign(UserController, UserController),
    PostController: Object.assign(PostController, PostController),
    ImagesController: Object.assign(ImagesController, ImagesController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    ProductController: Object.assign(ProductController, ProductController),
    TagController: Object.assign(TagController, TagController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    Trash: Object.assign(Trash, Trash),
}

export default Admin