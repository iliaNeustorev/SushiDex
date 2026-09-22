import PostTrashController from './PostTrashController'
import ProductTrashController from './ProductTrashController'
import CategoryTrashController from './CategoryTrashController'

const Trash = {
    PostTrashController: Object.assign(PostTrashController, PostTrashController),
    ProductTrashController: Object.assign(ProductTrashController, ProductTrashController),
    CategoryTrashController: Object.assign(CategoryTrashController, CategoryTrashController),
}

export default Trash