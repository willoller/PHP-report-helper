# Report helper

Makes outputting data a breeze.

1. Get your data

    /* transactions:
       +----------+--------+
       | name     | amount |
       +----------+--------+
       | Client A |    100 |
       | Client A |     65 |
       | Client A |     33 |
       | Client B |     27 |
       | Client B |    167 |
       +----------+--------+
    */
    $data = $pdo->query("SELECT * FROM transactions");

2. Place it in a new `Report`

    $report = new Report($data);

3. Output your data

    foreach ($report as $r) {
        echo "{$r->name} {$r->amount}\n";
    }

    echo "Total: {$report->total("amount")}";

Outputs:

    Client A 100
    Client A 65
    Client A 33
    Client B 27
    Client B 167
    Total: 392

## Breaking Out Data

More complicated reports can be generated as well.

    foreach ($report->name as $name => $data) {
        echo "# {$name}\n\n";

        foreach ($data as $row) {
            echo "{$r->amount}\n";
        }

        echo "{$name} Subtotal: {$data->total("amount")}\n\n";
    }

    echo "Grand Total: {$report->total("amount")}";

Outputs:

    # Client A

    100
    65
    33
    Client A Subtotal: 198

    # Client B

    27
    167
    Client B Subtotal: 194

    Grand Total: 392

# Extending

If the built-in functions are not sufficient, new functions can be added to the
report object using lambdas:

    $report->add('average', function($key) {
        $sum   = 0;
        $count = 0;

        foreach ($this->_data as $d) {
            $sum += $d[$key];
            $count++;
        }

        return $sum / $count;
    });

Then output your data same as always:

    foreach ($report->name as $name => $data) {
        echo "{$name} Average: {$data->average("amount")}\n\n";
    }

Outputs:

    Client A Average: 66
    Client B Average: 97

