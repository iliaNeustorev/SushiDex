import {computed} from 'vue';
import {usePage} from '@inertiajs/vue3';
import {Roles, type UserAuthResource} from '~types/generated';

export function useRoles() {
    const page = usePage<{ user: UserAuthResource | null }>();

    const roles = computed(() =>
        page.props.user?.roles.map(role => role.name) ?? []
    );

    function hasRole(role: Roles): boolean {
        return roles.value.includes(role);
    }

    const isAuthor = computed(() => hasRole(Roles.AUTHOR));

    const isModerator = computed(() =>
        hasRole(Roles.ADMIN) || hasRole(Roles.DEVELOPER)
    );

    const isDev = computed(() => hasRole(Roles.DEVELOPER));

    return {hasRole, isAuthor, isModerator, isDev};
}
