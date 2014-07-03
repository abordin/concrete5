<?
namespace Concrete\Core\Search\ItemList\Database;
use Concrete\Core\Search\StickyRequest;
use Database;
abstract class AttributedItemList extends ItemList
{

    abstract protected function getAttributeKeyClassName();

    /**
     * Filters by a attribute.
     */
    public function filterByAttribute($column, $value, $comparison = '=')
    {
        $this->filter('ak_' . $column, $value, $comparison);
    }

    /**
     * Magic method for setting up additional filtering by attributes.
     * @param $nm
     * @param $a
     * @throws \Exception
     */
    public function __call($nm, $a)
    {
        if (substr($nm, 0, 8) == 'filterBy') {
            $handle = uncamelcase(substr($nm, 8));
            if (count($a) == 2) {
                $this->filterByAttribute($attrib, $a[0], $a[1]);
            } else {
                $this->filterByAttribute($attrib, $a[0]);
            }
        } else {
            if (substr($nm, 0, 6) == 'sortBy') {
                $handle = uncamelcase(substr($nm, 6));
                if (count($a) == 1) {
                    $this->sortBy($attrib, $a[0]);
                } else {
                    $this->sortBy($attrib);
                }
            } else {
                throw new \Exception(t('%s method does not exist for the %s class', $nm, get_called_class()));
            }
        }
    }

    /**
     * @param StickyRequest $request
     */
    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        // First, we check to see if there are any sortable attributes we can add to the
        // auto sort columns.
        if (is_callable(array($this->getAttributeKeyClassName(), 'getList'))) {
            $l = call_user_func(array($this->getAttributeKeyClassName(), 'getList'));
            foreach($l as $ak) {
                $this->autoSortColumns[] = 'ak_' . $ak->getAttributeKeyHandle();
            }
        }

        parent::setupAutomaticSorting();
    }

}