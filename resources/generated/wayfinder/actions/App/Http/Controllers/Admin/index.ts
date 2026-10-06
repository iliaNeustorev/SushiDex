import OrderController from './OrderController'
import UserController from './UserController'
import PostController from './PostController'
import ImagesController from './ImagesController'
import DashboardController from './DashboardController'
import TagController from './TagController'
import CategoryController from './CategoryController'
import Trash from './Trash'
import ProductController from './ProductController'

const Admin = {
    OrderController: Object.assign(OrderController, OrderController),
    UserController: Object.assign(UserController, UserController),
    PostController: Object.assign(PostController, PostController),
    ImagesController: Object.assign(ImagesController, ImagesController),
    DashboardController: Object.assign(DashboardController, DashboardController),
    TagController: Object.assign(TagController, TagController),
    CategoryController: Object.assign(CategoryController, CategoryController),
    Trash: Object.assign(Trash, Trash),
    ProductController: Object.assign(ProductController, ProductController),
}

export default Admin