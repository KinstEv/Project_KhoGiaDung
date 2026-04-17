<?php
// Lightweight wrapper to integrate dvdoug/boxpacker with the application.
// The wrapper is defensive: it checks whether the BoxPacker classes exist and
// returns well-structured results. After running `composer install`, this
// class will attempt to create Packer/Box/Item objects and pack items into a
// single box created from a `vitri`'s inner dimensions.

class BoxPackerWrapper {
    private $available = false;

    public function __construct() {
        $this->available = class_exists('DVDoug\\BoxPacker\\Packer');
    }

    // Return whether boxpacker is available via composer autoload
    public function isAvailable() {
        return $this->available;
    }

    /**
     * Try to pack given items into the vitri (single box equal to vitri inner dims).
     *
     * Parameters:
     *  - $vitri: array with keys 'maViTri','daiToiDa','rongToiDa','caoToiDa'
     *  - $items: array of items, each with keys 'id'|'maHH', 'chieuDai','chieuRong','chieuCao', 'soLuong'
     *
     * Returns array:
     *  - success: bool
     *  - message: string
     *  - packed: associative details (raw packer output if available)
     */
    public function packIntoVitri($vitri, $items) {
        if (!$this->available) {
            return ['success' => false, 'message' => 'BoxPacker not installed (composer install missing)', 'packed' => null];
        }

        try {
            // Lazy-load classes
            $packerClass = 'DVDoug\\BoxPacker\\Packer';
            $packer = new $packerClass();

            // Box class choices (TestBox is commonly available in examples)
            if (class_exists('DVDoug\\BoxPacker\\TestBox')) {
                $boxClass = 'DVDoug\\BoxPacker\\TestBox';
            } else if (class_exists('DVDoug\\BoxPacker\\Box')) {
                $boxClass = 'DVDoug\\BoxPacker\\Box';
            } else {
                return ['success' => false, 'message' => 'Box class not found in BoxPacker package', 'packed' => null];
            }

            // Normalise vitri dimensions (BoxPacker expects width/length/depth ints)
            $vt_d = max(1, (int)($vitri['daiToiDa'] ?? 0));
            $vt_r = max(1, (int)($vitri['rongToiDa'] ?? 0));
            $vt_h = max(1, (int)($vitri['caoToiDa'] ?? 0));

            // Construct a box using vitri inner dims. Different Box constructors exist across versions;
            // try common signatures. We'll attempt multiple constructor argument shapes.
            $box = $this->instantiateBox($boxClass, $vitri, $vt_d, $vt_r, $vt_h);
            if ($box === null) {
                return ['success' => false, 'message' => 'Unable to instantiate BoxPacker box with vitri dimensions', 'packed' => null];
            }

            // Add the box to packer
            if (method_exists($packer, 'addBox')) {
                $packer->addBox($box);
            } else {
                // Older/newer APIs may differ; try to call addBox anyway and rely on exception handling
                $packer->addBox($box);
            }

            // Item class
            if (class_exists('DVDoug\\BoxPacker\\TestItem')) {
                $itemClass = 'DVDoug\\BoxPacker\\TestItem';
            } else if (class_exists('DVDoug\\BoxPacker\\Item')) {
                $itemClass = 'DVDoug\\BoxPacker\\Item';
            } else {
                return ['success' => false, 'message' => 'Item class not found in BoxPacker package', 'packed' => null];
            }

            // Add each item (with quantity) to the packer
            foreach ($items as $idx => $it) {
                $qty = max(1, (int)($it['soLuong'] ?? ($it['quantity'] ?? 1)));
                $id = isset($it['id']) ? $it['id'] : (isset($it['maHH']) ? $it['maHH'] . '-' . $idx : 'item-' . $idx);
                $w = max(1, (int)($it['chieuDai'] ?? $it['width'] ?? 0));
                $l = max(1, (int)($it['chieuRong'] ?? $it['length'] ?? 0));
                $h = max(1, (int)($it['chieuCao'] ?? $it['height'] ?? 0));

                // Instantiate item
                $itemObj = $this->instantiateItem($itemClass, $id, $w, $l, $h);
                if ($itemObj === null) {
                    return ['success' => false, 'message' => 'Unable to instantiate BoxPacker item for ' . $id, 'packed' => null];
                }

                // addItem may accept a quantity param
                if (method_exists($packer, 'addItem')) {
                    // Some packer APIs accept addItem($item, $quantity)
                    $ref = new ReflectionMethod($packer, 'addItem');
                    if ($ref->getNumberOfParameters() >= 2) {
                        $packer->addItem($itemObj, $qty);
                    } else {
                        // fallback: add item $qty times
                        for ($i = 0; $i < $qty; $i++) $packer->addItem($itemObj);
                    }
                } else {
                    // Fallback: try to call addItem and hope it works
                    for ($i = 0; $i < $qty; $i++) $packer->addItem($itemObj);
                }
            }

            // Execute packing
            if (!method_exists($packer, 'pack')) {
                return ['success' => false, 'message' => 'Packer->pack() method not available', 'packed' => null];
            }

            $packed = $packer->pack();

            // Interpret packed result: try to compute how many items were packed
            $packedCount = 0;
            $totalRequested = 0;
            foreach ($items as $it) $totalRequested += max(1, (int)($it['soLuong'] ?? ($it['quantity'] ?? 1)));

            // Packed can be an array of PackedBox objects
            foreach ($packed as $pb) {
                // PackedBox may have getItems() or getItems or items property
                if (method_exists($pb, 'getItems')) {
                    $packedItems = $pb->getItems();
                } elseif (property_exists($pb, 'items')) {
                    $packedItems = $pb->items;
                } else {
                    $packedItems = [];
                }

                foreach ($packedItems as $pi) {
                    // PackedItem may have getQuantity or quantity or getItem
                    if (method_exists($pi, 'getQuantity')) {
                        $packedCount += $pi->getQuantity();
                    } elseif (property_exists($pi, 'quantity')) {
                        $packedCount += (int)$pi->quantity;
                    } else {
                        // try to count as 1
                        $packedCount += 1;
                    }
                }
            }

            $success = ($packedCount >= $totalRequested);

            return ['success' => $success, 'message' => $success ? 'All items fit' : 'Not all items fit', 'packed' => $packed, 'packedCount' => $packedCount, 'requested' => $totalRequested];

        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage(), 'packed' => null];
        }
    }

    private function instantiateBox($boxClass, $vitri, $d, $r, $h) {
        // Try several common constructor signatures used by different versions/examples
        // 1) TestBox(string reference, int innerWidth, int innerLength, int innerDepth, int emptyWeight = 0, int maxWeight = null)
        try {
            return new $boxClass('vitri-' . ($vitri['maViTri'] ?? ''), $d, $r, $h, 0, null);
        } catch (\ArgumentCountError $e) {
            // try alternative signature: Box(reference, width, length, depth)
        } catch (\Throwable $e) {
            // ignore and try alternatives
        }

        try {
            return new $boxClass('vitri-' . ($vitri['maViTri'] ?? ''), $d, $r, $h);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function instantiateItem($itemClass, $id, $w, $l, $h) {
        // Try common constructors: TestItem(string reference, int width, int length, int depth, int weight = 0)
        try {
            return new $itemClass($id, $w, $l, $h, 0);
        } catch (\ArgumentCountError $e) {
            // try alternative without weight
        } catch (\Throwable $e) {
            // continue
        }

        try {
            return new $itemClass($id, $w, $l, $h);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
